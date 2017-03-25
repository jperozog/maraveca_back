from django.test import TestCase
from django.utils import timezone
from accounts.models import *
from django.utils.crypto import get_random_string
from random import randint
from accounts.tests import utils
from accounts.models import CLIENTE_REGISTRADO, Venezolano, SIN_IMPUESTO



class TestModels(TestCase, utils.Utils):

    def test_create_client(self):
        data_user = self.create_user()
        user = User.objects.create(**data_user)
        self.assertValues(data_user, user, set(['code_registry']))
        self.assertNotEqual(user.serializable_value('code_registry'), None)


