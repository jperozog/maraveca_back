from rest_framework import viewsets, permissions
from services import models as services_models
from services import serializers as services_serializers
from accounts.permissions import IsAdmin
# Create your views here.


class ServiceViewSet(viewsets.ModelViewSet):
    queryset = services_models.Service.objects.all()
    permission_classes = (permissions.IsAuthenticated, IsAdmin)
    serializer_class = services_serializers.ServiceSerializer

class PlanViewSet(viewsets.ModelViewSet):
    queryset = services_models.Plan.objects.all()
    permission_classes = (permissions.IsAuthenticated, IsAdmin)
    serializer_class = services_serializers.PlanSerializer

class AdditionalViewSet(viewsets.ModelViewSet):
    queryset = services_models.Additional.objects.all()
    permission_classes = (permissions.IsAuthenticated, IsAdmin)
    serializer_class = services_serializers.AdditionalSerializer

class ServerViewSet(viewsets.ModelViewSet):
    queryset = services_models.Server.objects.all()
    #permission_classes = (permissions.IsAuthenticated, IsAdmin)
    serializer_class = services_serializers.ServerSerializer

class CeldaViewSet(viewsets.ModelViewSet):
    queryset = services_models.Celda.objects.all()
    #permission_classes = (permissions.IsAuthenticated, IsAdmin)
    serializer_class = services_serializers.CeldaSerializer
