from django.test import TestCase
from accounts.models import *
from accounts.views import *
from accounts.tests import utils
from rest_framework.test import force_authenticate
from rest_framework.test import APITestCase, APIRequestFactory

class TestViews(APITestCase, utils.Utils):

    def setUp(self):
        self.create_user_staff()


    def test_views_client(self):
        """Registro de clientes.
        crear un cliente registrado
        """
        #acceso no autorizado
        request = self.create_request_post(self.create_user(), '/client/')
        response = ClientViewSet.as_view({'post':'list'})(request).render()
        self.assertEquals(response.status_code, 401, response.data)

        #acceso autorizado
        user = self.user
        force_authenticate(request, user)
        response = ClientViewSet.as_view({'post': 'list'})(request).render()
        self.assertEquals(response.status_code, 200, response.data)

    def test_get_client(self):
        user = User.objects.create(**self.create_user())
        request = self.create_request_get()
        response = ClientViewSet.as_view({'get': "retrieve"})(request, pk=user.id).render()
        self.assertEquals(response.status_code, 401)


        request = self.create_request_get_auth()
        response = ClientViewSet.as_view({'get': "retrieve"})(request, pk=user.id).render()
        self.assertEquals(response.status_code, 200)




