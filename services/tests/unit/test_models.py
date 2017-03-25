# encoding:utf-8
from services.models import Service
from services.models import *
from rest_framework.test import APITestCase
from django.utils import timezone
from accounts.models import User


def create_plan():
    data = {
        'name': 'name_plan',
        'price': 123.32,
        'monthly_type_plan': DEFINIDA_EN_PLANES
    }
    plan = Plan.objects.create(**data)
    return plan

def create_client():
    return User.objects.create(username="rr@gg.cc", password="123456")


class ServicesTest(APITestCase):

    def test_plan_create(self):
        data = {
            'name': 'name_plan',
            'price': 123.32,
            'monthly_type_plan': DEFINIDA_EN_PLANES
        }
        plan = Plan.objects.create(**data)
        for field in data.keys():
            self.assertEqual(data[field], plan.serializable_value(field))

    def test_service_create(self):
        data = {
            "plan": create_plan(),
            "notificatios_method": SMS_EMAIL,
            "install_date": timezone.now(),
            "install_type": INALAMBRICA,
            "monthly_type": DEFINIDA_EN_PLANES,
            "invoice_date": timezone.now(),
            "credit_days": 10,
            "cut_method": MODULO_SM,
            "install_price": 123.45,
            "start_date": timezone.now(),
            "monthly_cuote": 1234,
            "payment_method": OTHER,
            "comments": "coments and more comments",
            "cut_days": 5,
            "client": create_client()
        }
        service = Service.objects.create(**data)
        for field in list(set(data.keys()) - set(['client'])):
            self.assertEqual(data[field], service.serializable_value(field))
        self.assertEqual(data['client'].id, service.client.id)
