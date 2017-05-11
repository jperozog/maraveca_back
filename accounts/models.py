from __future__ import unicode_literals
from django.contrib.auth.models import AbstractUser
from django.db.models import signals
from django.db import models
from django.utils.crypto import get_random_string
from django.conf import settings
# Create your models here.

CLIENTE_POTENCIAL = 'cp'
CLIENTE_REGISTRADO = 'cr'
NINGUNO = 'no'

TYPE_USER = (
    (CLIENTE_POTENCIAL, 'Cliente potencial'),
    (CLIENTE_REGISTRADO, 'Cliente Registrado'),
    (NINGUNO, 'Ninguno'),
)

SIN_IMPUESTO = 1
CON_IMPUESTO = 2

SERIES = (
    (SIN_IMPUESTO, 'Sin impuesto'),
    (CON_IMPUESTO, 'Con impuesto'),
)

Extranjero = 'E'
Venezolano = 'V'
RIF = 'J'
GUBERNAMENTAL = 'G'

TYPE_DNI = (
    (Extranjero, 'Extranjero'),
    (Venezolano, 'Venezolano'),
    (RIF, 'RIF'),
    (GUBERNAMENTAL, 'Gubernamental'),
)

class ContactPeople(models.Model):
    first_name = models.CharField(max_length=40, blank=True, null=True)
    last_name = models.CharField(max_length=40, blank=True, null=True)
    phone = models.CharField(max_length=15, blank=True, null=True)
    description = models.TextField(blank=True, null=True)

class User(AbstractUser):

    phone = models.CharField(max_length=15, blank=True, null=True)
    phone2 = models.CharField(max_length=15, blank=True, null=True)
    address = models.TextField(blank=True, null=True)
    equipment = models.CharField(max_length=140, blank=True, null=True)
    type_user = models.CharField(max_length=2, choices=TYPE_USER,
                                 default=CLIENTE_POTENCIAL)
    type_dni = models.CharField(max_length=1, choices=TYPE_DNI, blank=True, null=True)
    dni = models.IntegerField(null=True)
    service = models.CharField(max_length=140, blank=True, null=True)
    birthdate = models.DateTimeField(blank=True, null=True)
    series = models.IntegerField(choices=SERIES, blank=True, null=True)
    comments = models.TextField(blank=True, null=True)
    description = models.TextField(blank=True, null=True)
    email = models.EmailField(unique=True)
    recovery = models.CharField(max_length=10, blank=True, null=True)
    code_registry = models.CharField(max_length=20, blank=True, null=True)
    contact_people = models.OneToOneField('ContactPeople', blank=True, null=True)

    USERNAME_FIELD = 'email'
    REQUIRED_FIELDS = []

    def __str__(self):
        return self.get_full_name()


def generate_code_registry():
    while True:
        code = get_random_string(length=8, allowed_chars="0123456789ABCDEF")
        if not User.objects.filter(code_registry=code).exists():
            return code


def send_email_registry(sender, instance, created, **kwargs):
    if created:
        code = generate_code_registry()
        instance.code_registry = code
        instance.save()
        subject = "Completa tu registro en Maraveca"
        api = settings.API
        api = "{0}/signup/?code={1}&?id={2}".format(api, code, instance.id)
        message = "completa tu registro aqui\n {}".format(api)
        try:
            instance.email_user(subject, message)
        except:
            pass


signals.post_save.connect(send_email_registry, sender=User)

