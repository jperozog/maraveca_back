from django.test import TestCase
from accounts.models import *
from services.views import *
from services.models import *
from accounts.tests import utils
from rest_framework.test import force_authenticate
from unittest.mock import MagicMock
from django.utils import timezone

class TestViews(TestCase, utils.Utils):

    def setUp(self):
        self.create_user_staff()

    def test_create_service(self):
        celda = Celda(name="celda")
        celda.save()
        server = Server(name="server")
        server.save()
        data = {
            'plan_id': 111,
            'notificatios_method': NOTIFICATIONS_METHOD,
            'install_date': timezone.now(),
            'install_type': SMS_EMAIL,
            'monthly_type': DEFINIDA_EN_PLANES,
            'invoice_date': timezone.now(),
            'credit_days': 10,
            'cut_method': MODULO_SM,
            'install_price': 123456,
            'start_date': timezone.now(),
            'monthly_cuote': 1565,
            'payment_method': OTHER,
            'comments': get_random_string(length=140),
            'cut_days': 25,
            'client': self.create_client(),

            "address": "av 54 con calle 84 san Rafael",
            "phoneSMS": "04141234567",
            "phones": "04241234567 042698765432",
            "server_id": server.id,
            "type_ip": "DI",
            "celdaAP_id": celda.id,
            "equipment": "equipo",
            "email_alt": "email@mail.com",
            "so": "01",
            "ip": "196.168.0.1",
            "mac": "255.255.255.255",
            "serial":"ABC123",
            "comments": "comentarios"
        }
        

        request = self.create_request_post(data)

        #acceso no autorizado
        response = ServiceViewSet.as_view({'post':'list'})(request).render()
        self.assertEquals(response.status_code, 401, response.data)

        request = self.create_request_post_auth(data, '/service/')
        response = ServiceViewSet.as_view({'post': 'list'})(request).render()
        self.assertEquals(response.status_code, 200, response.data)
        data['additional_id'] = [ 1 ]


    def test_create_plan(self):
        data = self.create_plan()

        request = self.create_request_post(data, '/plan/')
        self.viewset_test(data, 'post', request, 'list', PlanViewSet)




    def test_create_additional(self):
        data = self.create_additional()

        request = self.create_request_post(data, '/additional/')

        #acceso no autorizado
        view = 'AdditionalViewSet'
        method = 'post'
        type_method = 'list'

        self.viewset_test(data, method, request, type_method, AdditionalViewSet)

