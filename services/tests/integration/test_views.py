from django.test import TestCase
from accounts.models import *
from services.views import *
from services.models import *
from accounts.tests import utils
from rest_framework.test import force_authenticate

class TestViews(TestCase, utils.Utils):

    def setUp(self):
        self.user = self.create_user_staff()

    def test_create_service(self):
        #acceso no autorizado
        request = self.create_request_post()
        response = ServiceViewSet.as_view({'post':'list'})(request).render()
        self.assertEquals(response.status_code, 401, response.data)

        user = self.user
        force_authenticate(request, user)
        response = ServiceViewSet.as_view({'post': 'list'})(request).render()
