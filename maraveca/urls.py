"""maraveca URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/1.10/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  url(r'^$', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  url(r'^$', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.conf.urls import url, include
    2. Add a URL to urlpatterns:  url(r'^blog/', include('blog.urls'))
"""
from django.conf.urls import url, include
from django.contrib import admin
from rest_framework import routers
from accounts import views as accounts_views
from services import views as services_views

router = routers.DefaultRouter()

router.register('client', accounts_views.ClientViewSet)
router.register('potential', accounts_views.PotentialClientViewSet)
router.register('signup', accounts_views.SignUpViewSet)
router.register('service', services_views.ServiceViewSet)
router.register('plan', services_views.PlanViewSet)
router.register('additional', services_views.AdditionalViewSet)
router.register('server', services_views.ServerViewSet)
router.register('celda', services_views.CeldaViewSet)

urlpatterns = [
    url(r'^admin/', admin.site.urls),
    url(r'^docs/', include('rest_framework_docs.urls')),
    url(r'^login/', accounts_views.obtain_auth_token, name='login'),
    url(r'^', include(router.urls))
]
from django.conf import settings
from django.views.static import serve

if settings.DEBUG:
    urlpatterns += [
        url(r'^media/(?P<path>.*)$', serve, {'document_root': settings.MEDIA_ROOT, }),
    ]

