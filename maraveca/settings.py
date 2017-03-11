"""
Django settings for doorwin project.

Generated by 'django-admin startproject' using Django 1.10.4.

For more information on this file,see
https://docs.djangoproject.com/en/1.10/topics/settings/

For the full list of settings and their values,see
https://docs.djangoproject.com/en/1.10/ref/settings/
"""

import os
import sys

import environ

env = environ.Env()
environ.Env.read_env()
# Build paths inside the project like this: os.path.join(BASE_DIR,...)
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/1.10/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = '77ka$fm^p4+c!j2bkw-b77@tsd_a%o&^16ptk*00l7yx1t*p3#'

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = True
if env('DEBUG') == 'True':
    DEBUG = True
elif env('DEBUG') == 'False':
    DEBUG = False

API = env('API')

ALLOWED_HOSTS = ["*"]


# Application definition

INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'accounts',
    'rest_framework',
    'rest_framework_docs',
    'rest_framework.authtoken',
    'django_filters',
    'corsheaders',
]
REST_FRAMEWORK_DOCS = {
    'HIDE_DOCS': False  # Default: False
}

MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'corsheaders.middleware.CorsMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
]


CORS_ORIGIN_ALLOW_ALL = True

ROOT_URLCONF = 'maraveca.urls'

TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [],
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.debug',
                'django.template.context_processors.request',
                'django.contrib.auth.context_processors.auth',
                'django.contrib.messages.context_processors.messages',
            ],
        },
    },
]

WSGI_APPLICATION = 'maraveca.wsgi.application'


# Database
# https://docs.djangoproject.com/en/1.10/ref/settings/#databases

DATABASES = {
    'default': {
        # 'ENGINE': 'django.db.backends.postgresql_psycopg2',
        'ENGINE': 'django.db.backends.'+env('DB_BACKEND'),
        'NAME': env('DB_NAME'),
        'USER': env('DB_USER'),
        'HOST': env('DB_HOST'),
        'PASSWORD': env('DB_PASSWORD')
    }
}


# Password validation
# https://docs.djangoproject.com/en/1.10/ref/settings/#auth-password-validators

AUTH_PASSWORD_VALIDATORS = [
    {
        'NAME': 'django.contrib.auth.password_validation.UserAttributeSimilarityValidator',
    },
    {
        'NAME': 'django.contrib.auth.password_validation.MinimumLengthValidator',
    },
    {
        'NAME': 'django.contrib.auth.password_validation.CommonPasswordValidator',
    },
    {
        'NAME': 'django.contrib.auth.password_validation.NumericPasswordValidator',
    },
]


# Internationalization
# https://docs.djangoproject.com/en/1.10/topics/i18n/

LANGUAGE_CODE = 'es_ES'

TIME_ZONE = 'UTC'

USE_I18N = True

USE_L10N = True

USE_TZ = True

REST_FRAMEWORK = {
    'DEFAULT_AUTHENTICATION_CLASSES': (    
        'rest_framework.authentication.BasicAuthentication',
        'rest_framework.authentication.SessionAuthentication',
        'rest_framework.authentication.TokenAuthentication',
    ),
    'DEFAULT_FILTER_BACKENDS': ('rest_framework.filters.DjangoFilterBackend',)
}

AUTH_USER_MODEL="accounts.User"
# Static files (CSS,JavaScript,Images)
# https://docs.djangoproject.com/en/1.10/howto/static-files/

STATIC_URL = '/static/'
STATIC_ROOT = 'static/'

MEDIA_URL = '/media/'
MEDIA_ROOT = 'media/'

EMAIL_HOST = 'smtp.gmail.com'
EMAIL_HOST_USER = 'anuvenmx@gmail.com'
EMAIL_HOST_PASSWORD = 'creaturi1o'
EMAIL_PORT = '587'
EMAIL_USE_TLS = True
EMAIL_USE_SSL = False

# Configuramos logging para imprimir todo en stdout

LOGGING = {
    'version': 1,
    'dissable_existing_loggers': False,

    'formatters': {
        'verbose': {
            'format': "[%(asctime)s] %(levelname)s [%(name)s:%(lineno)s] %(message)s",
            'datefmt': "%Y-%m-%d %H:%M:%S",
        },
        'simple': {
            'format': "%(levelname)s %(message)s",
        },
    },

    'handlers': {
        'console': {
            'level': 'INFO',
            'class': 'logging.StreamHandler',
            'stream': sys.stdout,
            'formatter': 'simple',
        },
        'mail_admins': {
            'level': 'ERROR',
            'class': 'django.utils.log.AdminEmailHandler',
            'include_html': True,
        },
    },

    'loggers': {
        '': {
            'handlers': ['mail_admins', 'console'],
            'level': 'DEBUG',
        }
    },
}
