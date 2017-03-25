from rest_framework import viewsets, permissions
from services import models as services_models
from services import serializers as services_serializers
# Create your views here.


class ServiceViewSet(viewsets.ModelViewSet):
    queryset = services_models.Service.objects.all()
    permissions_classes = (permissions.IsAuthenticated,)
    serializer_class = services_serializers.ServiceSerializer
