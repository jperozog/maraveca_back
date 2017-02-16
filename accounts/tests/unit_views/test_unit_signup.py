from rest_framework.test import APITestCase,APIRequestFactory
from rest_framework import status
from django.core.urlresolvers import reverse
from rest_framework import status
from ..resources import correct_user
from accounts.views import SignupView

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
        factory=APIRequestFactory()

        request = factory.post(reverse('signup'),user)
        request.data=user
        response = SignupView.as_view()(request).render()
        self.assertEqual(response.status_code,status.HTTP_200_OK) 
        assert not 'password' in response.data.keys()

        user['password']='hobox1o1'
        user['username']='username1'
        request.data=user   
        request.POST=user     

        response = SignupView.as_view()(request).render()
        user.pop('password')
        self.assertEqual(response.status_code,status.HTTP_200_OK,response)
        assert response.data.get('id') 
        response.data.pop('id')     
        self.assertEqual(response.data,user)


