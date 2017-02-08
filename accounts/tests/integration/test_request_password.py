from rest_framework.test import APITestCase
from ..resources import create_user, correct_user, assign_code_recovery, code
from accounts.models import User

class RequestPassword(APITestCase):

	def setUp(self):
		self.user = create_user()

	def test_input_email(self):
		response = self.client.post('/email-request/',{"email":"email@email.com"})
		self.assertEqual(response.status_code,200)
		
		self.assertEqual(response.data,{'detail':'send'})

		user=User.objects.get(id=1)
		assert user.recovery


	def test_input_email_not_exists(self):
		response = self.client.post('/email-request/',{"email":"email@esmail.com"})
		self.assertEqual(response.status_code,400)

	def test_input_code(self):
		assign_code_recovery(self.user,code)
		response = self.client.post('/verify-code/',{'code':code})
		self.assertEqual(response.status_code,200)

	def test_change_password(self):
		assign_code_recovery(self.user,code)
		data = {
		    'code':code,
		    'password': 'hobox1o1'
		}
		response = self.client.post('/change-password/',data)
		self.assertEqual(response.status_code,200)
		data = {
		    'code':'3234',
		    'password': 'hobox1o1'
		}
		response = self.client.post('/change-password/',data)
		self.assertEqual(response.status_code,400)


		response = self.client.login(username=correct_user['username'],password='hobox1o1')
		self.assertEqual(response,True,response)
