# encoding:utf-8
from __future__ import unicode_literals

from django.db import models

# Create your models here.

SMS_EMAIL = "me"

NOTIFICATIONS_METHOD = (
    (SMS_EMAIL, "SMS y Email"),
)

INALAMBRICA = "in"

INSTALL_TYPE = (
    (INALAMBRICA, "in"),
)

DEFINIDA_EN_PLANES = "dp"

MONTHLY_TYPE = (
    (DEFINIDA_EN_PLANES, "in"),
)

MODULO_SM = "sm"

CUT_METHOD = (
    (MODULO_SM, "Modulo SM"),
)

OTHER = "ot"

PAYMENT_METHOD = (
    (OTHER, "ot"),
)


class Plan(models.Model):
    """Planes de internet.

    Acá se almacenan los datos de los planes
    ofrecidos a los clientes

    """

    name = models.CharField(max_length=50)
    price = models.DecimalField(max_digits=10, decimal_places=2)
    monthly_type_plan = models.CharField(max_length=2, choices=MONTHLY_TYPE)

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


class Service(models.Model):
    """Tabla de servicios.

    Servicios de internet

    """

    plan = models.ForeignKey('Plan')
    additional = models.ManyToManyField('Additional')
    notificatios_method = models.CharField(max_length=2,
                                           choices=NOTIFICATIONS_METHOD)
    install_date = models.DateTimeField()
    install_type = models.CharField(max_length=2, choices=INSTALL_TYPE)
    monthly_type = models.CharField(max_length=2, choices=MONTHLY_TYPE)
    invoice_date = models.DateTimeField()
    credit_days = models.IntegerField()
    cut_method = models.CharField(max_length=2, choices=CUT_METHOD)
    install_price = models.DecimalField(max_digits=10, decimal_places=2)
    start_date = models.DateTimeField()
    monthly_cuote = models.DecimalField(max_digits=10, decimal_places=2)
    payment_method = models.CharField(max_length=2, choices=PAYMENT_METHOD)
    comments = models.TextField(blank=True, null=True)
    cut_days = models.IntegerField()
    client = models.ForeignKey('accounts.User', related_name='services')

    def __unicode__(self):
        return self.plan.name

    def __str__(self):
        return self.plan.name
