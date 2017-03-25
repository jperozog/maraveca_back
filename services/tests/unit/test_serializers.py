# encoding:utf-8
from services.models import Service
from services.models import *
from rest_framework.test import APITestCase
from django.utils import timezone
from accounts.models import User
from accounts.tests import  utils
from services.serializers import *


class TestSerializers(APITestCase, utils.Utils):


    def test_read_plan(self):
        data = self.create_plan()
        plan = Plan.objects.create(**data)
        serializer = PlanSerializer(plan)
        self.assertDict(data, serializer.data)