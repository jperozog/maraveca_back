# -*- encoding:utf-8 -*-
from rest_framework import (views, viewsets, permissions,
                            filters, mixins)
from rest_framework.response import Response
from accounts import serializers as accounts_serializers
from accounts import models as accounts_models
from django.conf import settings
import random
import string
from django.core.mail.message import EmailMultiAlternatives
from django.template.loader import render_to_string
from accounts.permissions import IsSelf
from rest_framework.authtoken.views import ObtainAuthToken
from django.db.models import Q
from rest_framework.decorators import detail_route

obtain_auth_token = ObtainAuthToken.as_view(
    serializer_class=accounts_serializers.AuthTokenSerializer
)


class SendMail:
    @detail_route(methods=['post'], permission_classes=[permissions.IsAuthenticated],
                  url_path='send-email')
    def send_email(self, request, *args, **kwargs):
        if not request.data.get("subject", None):
            return Response("el campo 'subject' es requerido", status=400)

        if not request.data.get("message", None):
            return Response("el campo 'message' es requerido", status=400)

        subject = request.data.get("subject")
        message = request.data.get("message")
        data = {
            "subject": subject,
            "message": message
        }
        html_content = render_to_string("email/message.html", data)

        instance = self.get_object()
        instance.email_user(subject=subject, message=message,
                            from_email=settings.EMAIL_HOST_USER,
                            html_message=html_content)
        return Response({"success": True}, status=200)


class SignUpViewSet(mixins.RetrieveModelMixin, mixins.UpdateModelMixin,
                    viewsets.GenericViewSet, SendMail):
    queryset = accounts_models.User.objects.all()
    serializer_class = accounts_serializers.SignupSerializer
    filter_backends = (filters.DjangoFilterBackend,)
    filter_fields = ('code_registry',)
    http_method_names = ['get', 'put']

    def retrieve(self, request, *args, **kwargs):
        instance = self.get_object()
        if request.data.get('code_registry') == instance.code_registry:
            serializer = self.get_serializer(instance)
            return Response(serializer.data, status=200)
        else:
            return Response(u"Se requiere un codigo valido", status=405)


class ClientViewSet(viewsets.ModelViewSet, SendMail):
    queryset = accounts_models.User.objects.all()
    serializer_class = accounts_serializers.ClientSerializer
    permission_classes = (permissions.IsAuthenticated, IsSelf,)

    def list_user(self, request):
        queryset = self.filter_queryset(self.get_queryset())
        queryset = queryset.filter(id=request.user.id)
        serializer = self.get_serializer(queryset, many=True)
        return serializer.data[0]

    def get_queryset(self):
        queryset = super(ClientViewSet, self).get_queryset()
        client_id = accounts_models.CLIENTE_REGISTRADO
        queryset = queryset.filter(type_user=client_id, is_staff=False)
        return queryset


class PotentialClientViewSet(viewsets.ModelViewSet):
    queryset = accounts_models.User.objects.all()
    serializer_class = accounts_serializers.PotentialClientSerializer
    permission_classes = (permissions.IsAuthenticated, permissions.IsAdminUser)

    def get_queryset(self):
        queryset = super(PotentialClientViewSet, self).get_queryset()
        client_id = accounts_models.CLIENTE_POTENCIAL
        queryset = queryset.filter(type_user=client_id)
        return queryset

    @detail_route(methods=['put'],
                  permission_classes=(permissions.IsAuthenticated,),
                  url_path="convert-to-client")
    def convert_to_client(self, request, pk=None):
        instance = self.get_object()
        serializer = accounts_serializers.ClientSerializer
        data = {}
        fields_client = accounts_serializers.ClientSerializer.Meta.fields
        fields_potential = accounts_serializers.ClientSerializer.Meta.fields
        fields = list(set(fields_client) & set(fields_potential))
        for field in fields:
            try:
                data[field] = getattr(instance, field)
            except:
                pass
        for field in request.data.keys():
            if request.data.get(field):
                data[field] = request.data.get(field)
        instance.type_user = accounts_models.CLIENTE_REGISTRADO
        instance.save()
        serializer = serializer(instance, data=data,
                                context={'request': request})
        serializer.is_valid(raise_exception=True)
        serializer.save()
        return Response(serializer.data, status=200)


def generate_code(num_digits=6):
    return ''.join([random.choice(string.digits) for _ in range(num_digits)])


def passwordRecovery(user_id):
    try:
        user = accounts_models.User.objects.get(id=user_id)
        subject, from_email, to = (u'Recuperar contraseña.',
                                   settings.EMAIL_HOST_USER, user.email)

        text_content = render_to_string("email/recovery_password.html",
                                        {"code": user.recovery})

        html_content = render_to_string("email/recovery_password.html",
                                        {"code": user.recovery})

        msg = EmailMultiAlternatives(subject, text_content, from_email, [to])
        msg.attach_alternative(html_content, "text/html")
        msg.send()
        return True
    except Exception as exc:
        return exc


class EmailRecoveryPasswordView(views.APIView):
    def post(self, request, format=None):
        email = request.data.get('email')
        user = accounts_models.User.objects.filter(email=email)
        if user:
            user = user[0]
            user.recovery = generate_code()
            user.save()
            response = passwordRecovery(user.id)
            if response is True:
                return Response({'detail': 'send'}, status=200)
            else:
                return Response({'detail': response}, status=400)

        else:
            return Response({'detail': u'El email no existe'}, status=400)


class VerifyCodeView(views.APIView):
    def post(self, request, format=None):
        code = request.data.get('code')
        user = accounts_models.User.objects.filter(recovery=code)
        if code and user.exists():
            return Response({'detail': u'si'}, status=200)
        else:
            return Response({'detail': u'Código invalido'}, status=400)


class ChangePasswordRecoveryView(views.APIView):
    def post(self, request, format=None):
        code = request.data.get('code')
        user = accounts_models.User.objects.filter(recovery=code)
        if code and user and request.data.get('password'):
            user = user[0]
            Profile = accounts_serializers.ProfileSerializer
            serializer = Profile(user, data=request.data,
                                 fields=('password',), partial=True)
            serializer.is_valid(raise_exception=True)
            serializer.save()
            user = serializer.instance
            user.recovery = ""
            user.save()
            return Response(serializer.data, status=200)
        else:
            return Response({'detail': u'Cambio invalido'}, status=400)
