from django.test import TestCase
from accounts.models import *
from accounts.serializers import *
from accounts.tests import utils


class TestSerializers(TestCase, utils.Utils):

    def test_create_user(self):
        user = self.create_user()
        serializer = UserSerializer(data=user)
        serializer.is_valid(raise_exception=True)
        serializer.save()
        self.assertValues(user, serializer.instance, excepts=['password'])
        self.assertNotEquals(serializer.instance.password, None)

    def test_update_user(self):
        user1 = self.create_user()
        user2 = self.create_user()
        user_model1 = User.objects.create(**user1)

        serializer = UserSerializer(user_model1, data=user2)
        serializer.is_valid(raise_exception=True)
        serializer.save()

        self.assertValues(user2, serializer.instance, ['password'])

    def test_read_user(self):
        user = self.create_user()
        user_model1 = User.objects.create(**user)

        serializer = UserSerializer(user_model1)
        self.assertDict(user, serializer.data, ['id', 'birthdate', 'password'])





