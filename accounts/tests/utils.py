from django.utils import timezone
from accounts.models import *
from services.models import *
from django.utils.crypto import get_random_string
from random import randint
from rest_framework.test import APITestCase, APIRequestFactory
from rest_framework.test import force_authenticate


class Utils:
    def create_user(self):
        data_user = {
            'phone': get_random_string(length=15),
            'phone2': get_random_string(length=15),
            'address': get_random_string(length=100),
            'equipment': get_random_string(length=140),
            'type_user': CLIENTE_REGISTRADO,
            'password': "hobox1o1",
            'username': get_random_string(length=15),
            'type_dni': Venezolano,
            'dni': randint(1000000, 9999999999),
            'service': 'hola',
            'birthdate': timezone.now(),
            'series': SIN_IMPUESTO,
            'comments': get_random_string(256),
            'email': 'hola@gmail.com',
            'recovery': get_random_string(length=10)
        }
        return data_user

    def assertValues(self, data_user, user, excepts=set([])):
        for field in list(set(data_user.keys()) - set(excepts)):
            self.assertEqual(data_user[field], user.serializable_value(field))


    def assertValuesSerializer(self, data_user, user, excepts=set([])):
        for field in list(set(data_user.keys()) - set(excepts)):
            self.assertEqual(data_user[field], user.get(field))

        for field in list(set(data_user.keys()) - set(excepts)):
            self.assertEqual(data_user[field], user.serializable_value(field))

    def assertDict(self, data_user, user, excepts=set([])):
        for field in list(set(data_user.keys()) - set(excepts)):
            self.assertEqual(data_user[field], user.get(field))


    def create_request_get_auth(self):
        #crear el request
        request = self.create_request_get()
        user = self.user
        force_authenticate(request, user)
        return request

    def create_request_get(self):
        #crear el request
        factory = APIRequestFactory()
        request = factory.get('/client/', {})
        return request

    def create_request_post_auth(self,data, url='/client/'):
        #crear el request
        request = self.create_request_post(data, url)
        user = self.user
        force_authenticate(request, user)
        return request

    def create_request_post(self, data, url='/client/'):
        #crear el request
        factory = APIRequestFactory()
        request = factory.post(url, data)
        return request


    def create_user_staff(self):
        self.user = User(username="user", password="password")
        self.user.is_staff = True
        self.user.save()

    def create_plan(self):
        data = {
            'name': get_random_string(length=50),
            'price': '123456.00',
            'monthly_type_plan': MONTHLY_TYPE
        }
        return data

    def create_additional(self):
        data = {
            'name': get_random_string(length=50),
            'price': 123456
        }
        return data

    def create_client(self):
        user = User(**self.create_user())
        user.save()
        return user


    def viewset_test(self, data, method, request, type_method, view):
        response = view.as_view({method: type_method})(request).render()
        self.assertEquals(response.status_code, 401, response.data)
        request = self.create_request_post_auth(data, '/additional/')
        response = view.as_view({method: type_method})(request).render()
        self.assertEquals(response.status_code, 200, response.data)