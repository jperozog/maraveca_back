# -*- encoding:utf-8 -*-
from rest_framework import serializers
from accounts import models as accounts_models
from django.contrib.auth.hashers import make_password
from django.utils.translation import ugettext_lazy as _
from django.contrib.auth import authenticate


class CustomSerializer(serializers.ModelSerializer):

    def __init__(self, *args, **kwargs):
        fields = kwargs.pop('fields', None)
        depth = kwargs.pop('depth', None)
        super(CustomSerializer, self).__init__(*args, **kwargs)
        if depth is not None:
            self.Meta.depth = depth
        if fields is not None:
            allowed = set(fields)
            existing = set(self.fields.keys())
            for field_name in existing-allowed:
                self.fields.pop(field_name)


class UserSerializer(CustomSerializer):

    class Meta:
        model = accounts_models.User
        fields = '__all__'
        extra_kwargs = {
            'type_user': {'required': False},
            'id': {'read_only': True},
            'password': {'write_only': True},
            'email': {'required': True}
        }

    def create(self, validated_data):
        instance = super(UserSerializer, self).create(validated_data)
        if validated_data.get('password'):
            instance.password = make_password(instance.password)
        return instance

    def update(self, instance, validated_data):
        instance = super(UserSerializer, self).update(instance, validated_data)
        if validated_data.get('password'):
            instance.password = make_password(instance.password)
        instance.save()
        return instance


class SignupSerializer(serializers.ModelSerializer):
    password = serializers.CharField(max_length=20, required=True,
                                     write_only=True)

    class Meta:
        model = accounts_models.User
        fields = ('id', 'email', 'password', 'code_registry',)
        extra_kwargs = {
            'email': {'read_only': True},
            'code_registry': {'required': True, 'write_only': True},
            'id': {'read_only': True}
        }

    def validate_code_registry(self, code_registry):
        user = accounts_models.User.objects.filter(code_registry=code_registry)
        if not user.exists():
            raise serializers.ValidationError(u'Código inválido')
        return code_registry

    def update(self, instance, validated_data):
        validated_data['username'] = instance.email
        instance = super(SignupSerializer, self).update(instance, validated_data)
        instance.password = make_password(instance.password)
        instance.save()
        return instance


class ClientSerializer(UserSerializer):
    series_display = serializers.SerializerMethodField()

    def get_series_display(self, obj):
        return obj.get_series_display() if obj.series else ''

    class Meta:
        model = accounts_models.User
        fields = ('id', 'phone', 'phone2', 'address', 'equipment', 'service',
                  'first_name', 'last_name','series', 'series_display', 'comments',
                  'email', 'date_joined', 'type_dni', 'dni', 'birthdate',)

        extra_kwargs = {
            'id': {'read_only': True},
            'email': {'required': True},
            'dni': {'required': True},
            'type_dni': {'required': True}
        }

    def create(self, validated_data):
        dni = validated_data.get('dni')
        type_dni = validated_data.get('type_dni')
        user = accounts_models.User.objects.filter(dni=dni, type_dni=type_dni)
        if user.exists():
            msg = 'el cliente ya fue registrado'
            raise serializers.ValidationError({'dni': msg})
        validated_data['username'] = validated_data.get('email')
        validated_data['type_user'] = accounts_models.CLIENTE_REGISTRADO
        instance = super(UserSerializer, self).create(validated_data)
        return instance

    def update(self, instance, validated_data):
        dni = validated_data.get('dni')
        type_dni = validated_data.get('type_dni')
        user = accounts_models.User.objects.filter(dni=dni, type_dni=type_dni)
        if user.exists() and user[0] != instance:
            msg = 'el cliente ya fue registrado'
            raise serializers.ValidationError({'dni': msg})
        validated_data['username'] = validated_data.get('email')
        validated_data['type_user'] = accounts_models.CLIENTE_REGISTRADO
        instance = super(UserSerializer, self).update(instance, validated_data)
        return instance


class PotentialClientSerializer(CustomSerializer):

    def get_series_display(self, obj):
        return obj.get_series_display()

    class Meta:
        model = accounts_models.User
        fields = ('id', 'phone', 'phone2', 'address', 'equipment', 'service',
                  'first_name', 'comments', 'email', 'date_joined')

        extra_kwargs = {
            'id': {'read_only': True},
            'email': {'required': True},
            'date_joined': {'read_only': True}
        }

    def create(self, validated_data):
        validated_data['username'] = validated_data.get('email')
        validated_data['type_user'] = accounts_models.CLIENTE_POTENCIAL
        instance = super(PotentialClientSerializer, self)
        instance = instance.create(validated_data)
        instance.is_active = False
        return instance


class AuthTokenSerializer(serializers.Serializer):
    email = serializers.CharField(label=_("Email"))
    password = serializers.CharField(label=_("Password"),
                                     style={'input_type': 'password'})

    def validate(self, attrs):
        email = attrs.get('email')
        password = attrs.get('password')

        if email and password:
            user = authenticate(username=email, password=password)

            if user:
                # From Django 1.10 onwards the `authenticate` call simply
                # returns `None` for is_active=False users.
                # (Assuming the default `ModelBackend` authentication backend.)
                if not user.is_active:
                    msg = _('User account is disabled.')
                    raise serializers.ValidationError(msg,
                                                      code='authorization')
            else:
                msg = _('Unable to log in with provided credentials.')
                raise serializers.ValidationError(msg, code='authorization')
        else:
            msg = _('Must include "username" and "password".')
            raise serializers.ValidationError(msg, code='authorization')

        attrs['user'] = user
        return attrs
