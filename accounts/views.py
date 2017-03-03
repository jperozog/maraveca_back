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

obtain_auth_token = ObtainAuthToken.as_view(
    serializer_class=accounts_serializers.AuthTokenSerializer
)


class SignUpViewSet(mixins.RetrieveModelMixin, mixins.UpdateModelMixin,
                    viewsets.GenericViewSet):
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


class ClientViewSet(viewsets.ModelViewSet):
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
        queryset = queryset.filter(type_user=client_id)
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
