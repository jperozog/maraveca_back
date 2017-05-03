# encoding:utf-8
from __future__ import unicode_literals

from django.db import models

# Create your models here.

EMAIL = "em"
SMS = "sm"
SMS_EMAIL = "se"

NOTIFICATIONS_METHOD = (
    (SMS, "SMS"),
    (EMAIL, "Email"),
    (SMS_EMAIL, "Email y SMS"),
)

INALAMBRICA = "in"

INSTALL_TYPE = (
    (INALAMBRICA, "Inalambrica"),
)

DEFINIDA_EN_PLANES = "dp"

MONTHLY_TYPE = (
    (DEFINIDA_EN_PLANES, "Definida en planes"),
)

MODULO_SM = "sm"

CUT_METHOD = (
    (MODULO_SM, "Modulo SM"),
)

OTHER = "ot"
TRANSFERENCIA = "tr"
DEPOSITO = "de"
CHEQUE = "ch"

PAYMENT_METHOD = (
    (OTHER, "otro"),
    (TRANSFERENCIA, "Transferencia"),
    (DEPOSITO, "Deposito"),
    (CHEQUE, "Cheque"),

)

SERVER = (
    ("01", "Server 1"),
    ("02", "Server 2"),
)

SO = (
    ("01", "S.O 1"),
    ("02", "S.O 2"),
)


CELDA_AP = (
    ("01", "Celda 1"),
    ("02", "Celda 2"),
)

ESTATICA = "ES"
DINAMICA = "DI"

TYPE_IP = (
    (ESTATICA, "Estatica"),
    (DINAMICA, "Dinamica"),
)

class Plan(models.Model):
    """Planes de internet.

    Acá se almacenan los datos de los planes
    ofrecidos a los clientes

    """

    name = models.CharField(max_length=50)
    price = models.DecimalField(max_digits=10, decimal_places=2)
    monthly_type_plan = models.CharField(max_length=2, choices=MONTHLY_TYPE, default=DEFINIDA_EN_PLANES)

    def __unicode__(self):
        return self.name

    def __str__(self):
        return self.name


class Additional(models.Model):
    """Servicios adicionales.

    Acá se almacenan los servicios adcionales
    que pueden agregarse a los planes contratados

    """

    name = models.CharField(max_length=50)
    price = models.DecimalField(max_digits=10, decimal_places=2)

    def __unicode__(self):
        return self.name

    def __str__(self):
        return self.name


class Server(models.Model):
    """Servicios adicionales.

    Acá se almacenan los servidores
    que pueden agregarse a los planes contratados

    """

    name = models.CharField(max_length=255)

    def __unicode__(self):
        return self.name

    def __str__(self):
        return self.name


class Celda(models.Model):
    """Servicios adicionales.

    Acá se almacenan las celdas
    que pueden agregarse a los planes contratados

    """

    name = models.CharField(max_length=255)

    def __unicode__(self):
        return self.name

    def __str__(self):
        return self.name

class Service(models.Model):
    """Tabla de servicios.

    Servicios de internet

    """

    plan = models.ForeignKey('Plan')
    additional = models.ManyToManyField('Additional', blank=True, null=True)
    notificatios_method = models.CharField(max_length=2,
                                           choices=NOTIFICATIONS_METHOD,
                                           default=EMAIL)
    install_date = models.DateTimeField(blank=True, null=True)
    install_type = models.CharField(max_length=2, choices=INSTALL_TYPE, blank=True, null=True)
    monthly_type = models.CharField(max_length=2, choices=MONTHLY_TYPE, blank=True, null=True)
    invoice_date = models.DateTimeField(blank=True, null=True)
    credit_days = models.IntegerField(blank=True, null=True)
    cut_method = models.CharField(max_length=2, choices=CUT_METHOD, blank=True, null=True)
    install_price = models.DecimalField(max_digits=10, decimal_places=2, blank=True, null=True)
    start_date = models.DateTimeField(blank=True, null=True)
    monthly_cuote = models.DecimalField(max_digits=10, decimal_places=2, blank=True, null=True)
    payment_method = models.CharField(max_length=2, choices=PAYMENT_METHOD, blank=True, null=True)
    comments = models.TextField(blank=True, null=True)
    cut_days = models.IntegerField(blank=True, null=True)
    client = models.ForeignKey('accounts.User', related_name='services')


    address = models.TextField(blank=True, null=True)
    phoneSMS = models.CharField(max_length=20, blank=True, null=True)
    phones = models.CharField(max_length=20, blank=True, null=True)
    server = models.ForeignKey("Server", blank=True, null=True)
    type_ip = models.CharField(max_length=2, choices=TYPE_IP, default=DINAMICA)
    celdaAP = models.ForeignKey("Celda", blank=True, null=True)
    equipment = models.CharField(max_length=20, blank=True, null=True)
    email_alt = models.EmailField(blank=True, null=True)
    so = models.CharField(max_length=20, choices=SO, blank=True, null=True)
    ip = models.CharField(max_length=20, blank=True, null=True)
    mac = models.CharField(max_length=20, blank=True, null=True)
    serial = models.CharField(max_length=20, blank=True, null=True)
    comments = models.TextField(blank=True, null=True)

    def __unicode__(self):
        return self.plan.name

    def __str__(self):
        return self.plan.name
