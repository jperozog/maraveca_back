from rest_framework.test import APITestCase
from rest_framework import status
from django.core.urlresolvers import reverse
from rest_framework import status
from ..resources import create_user, correct_user
# Create your tests here.
class Regiser(APITestCase):

    def setUp(self):
        self.user = {
                "username":"username",
                "password":"password",
                "first_name":"first_name",
                "last_name":"last_name",
                "type_user":"co",
                "email":"email@email.com",
                "phone":"+584241235566",
                "address":"60 av, 90 st wall street"
            }

    def test_signup(self):
        user = self.user
        user['username']="test12"
        response = self.client.post(reverse('signup'),user)
        self.assertEqual(response.status_code,status.HTTP_200_OK)


    def test_login(self): 
        data=self.user
        data['username']="test3"     
        user = create_user(data)
        test={'username':"test3","password":"password"}

        response = self.client.post(reverse('login'),test)
        self.assertEqual(response.status_code,status.HTTP_200_OK,response)