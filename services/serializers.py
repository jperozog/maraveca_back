from rest_framework import serializers
from services import models as services_models
from accounts.serializers import UserSerializer
from accounts import models as accounts_models


class AdditionalSerializer(serializers.ModelSerializer):

    class Meta:
        model = services_models.Additional
        fields = '__all__'


class PlanSerializer(serializers.ModelSerializer):
    monthly_type_plan_display = serializers.SerializerMethodField()

    def get_monthly_type_plan_display(self, obj):
        return obj.get_monthly_type_plan_display()

    class Meta:
        model = services_models.Plan
        fields = '__all__'


class ServiceSerializer(serializers.ModelSerializer):
    client = UserSerializer(read_only=True)
    notificatios_method = serializers.SerializerMethodField()
    install_type = serializers.SerializerMethodField()
    monthly_type = serializers.SerializerMethodField()
    cut_method = serializers.SerializerMethodField()
    payment_method = serializers.SerializerMethodField()
    plan_id = serializers.IntegerField()
    client_id = serializers.IntegerField()
    additional = AdditionalSerializer(many=True, required=False, read_only=True)
    additional_id = serializers.ListField(child=serializers.CharField(), write_only=True, required=False)
    server_display = serializers.SerializerMethodField()
    type_ip_display = serializers.SerializerMethodField()
    celdaAP_display = serializers.SerializerMethodField()

    def get_server_display(self, obj):
        return obj.get_server_display()

    def get_type_ip_display(self, obj):
        return obj.get_type_ip_display()

    def get_celdaAP_display(self, obj):
        return obj.get_celdaAP_display()

    def notificatios_method(self, obj):
        return obj.get_notificatios_method_display()

    def install_type(self, obj):
        return obj.get_install_type_display()

    def monthly_type(self, obj):
        return obj.get_monthly_type_display()

    def cut_method(self, obj):
        return obj.get_cut_method_display()

    def payment_method(self, obj):
        return obj.get_payment_method_display()

    def validate_plan_id(self, plan_id):
        plan = services_models.Plan.objects.filter(id=plan_id)
        if not plan.exists():
            msg = 'el plan seleccionado no existe'
            raise serializers.ValidationError(msg)
        return plan_id

    def validate_client_id(self, client_id):
        CLIENTE = accounts_models.CLIENTE_REGISTRADO
        client = accounts_models.User.objects.filter(id=client_id,
                                                     type_user=CLIENTE)
        if not client.exists():
            msg = 'el cliente seleccionado no existe'
            raise serializers.ValidationError(msg)
        return client_id

    class Meta:
        model = services_models.Service
        fields = "__all__"
        extra_kwargs = {
            'plan': {'read_only': True},
            'client': {'read_only': True},
            'additional': {'required': False},
        }
        depth = 1

    def create(self, validated_data):
        client_id = validated_data.pop('client_id')
        client = accounts_models.User.objects.get(id=client_id)
        validated_data['client'] = client
        plan_id = validated_data.pop('plan_id')
        plan = services_models.Plan.objects.get(id=plan_id)
        validated_data['plan'] = plan

        instance = super(ServiceSerializer, self).create(validated_data)
        return instance

    def update(self, instance, validated_data):
        client_id = validated_data.get('client_id')
        client = accounts_models.User.objects.get(id=client_id)
        validated_data['client'] = client

        plan_id = validated_data.get('plan_id')
        plan = services_models.Plan.objects.get(id=plan_id)
        validated_data['plan'] = plan

        instance = super(ServiceSerializer, self).update(instance,
                                                         validated_data)
        return instance
