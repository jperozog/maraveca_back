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

    def test_create_server(self):
        self.client.login(email=self.user.email,
                          password="password")
        data = self.create_server()
        response = self.client.post('/server/', data)
        self.assertEqual(response.status_code, 201, response.data)
        self.assertEqual(response.data.get('name'), data.get("name"))

    def test_create_celda(self):
        self.client.login(email=self.user.email,
                          password="password")
        data = self.create_server()
        response = self.client.post('/celda/', data)
        self.assertEqual(response.status_code, 201, response.data)
        self.assertEqual(response.data.get('name'), data.get("name"))
