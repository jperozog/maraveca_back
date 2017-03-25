from django.contrib import admin
from services import models
# Register your models here.

admin.site.register(models.Service)
admin.site.register(models.Plan)
admin.site.register(models.Additional)
