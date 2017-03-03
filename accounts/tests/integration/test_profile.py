from rest_framework.test import APITestCase
from rest_framework import status
from django.core.urlresolvers import reverse
from rest_framework import status
from ..resources import create_user, correct_user
from accounts.models import User
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
        User.objects.create_user(**self.user)
        self.client.login(username=self.user['username'],password=self.user['password'])


    def test_get_profile(self):
        response = self.client.get('/profile/')
        self.assertEqual(response.status_code,status.HTTP_200_OK)

    def test_edit_profile(self): 
        data=self.user
        data['password']='hobox1o1'
        test_data = {'username':'username','password':'password'}
        responseData=self.user
        user_id = User.objects.get(username=self.user['username']).id
        response = self.client.put(reverse('user-list')+"{0}/".format(user_id),data)
        self.assertEqual(response.status_code,status.HTTP_200_OK,response)
        response = self.client.post('/login/',data)
        self.assertEqual(response.status_code,status.HTTP_200_OK,response)