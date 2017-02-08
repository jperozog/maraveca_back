from accounts.models import User
from collections import OrderedDict

correct_user = {
    "username": "username",
    "password": "password",
    "first_name": "first_name",
    "last_name": "last_name",
    "type_user": "co",
    "email": "email@email.com",
    "phone": "+584241235566",
    "address": "60 av, 90 st wall street"
}
code = '123456'


def create_user(data=correct_user):
    user = User.objects.create_user(**data)
    user.save()
    return user


def assign_code_recovery(user, code='123456'):
    user.recovery = code
    user.save()
    return user

store = {
    'name': "company name",
    'longitude': '10.23.12.445',
    'latitude': '23.323.342.3',
}

storeTest = store
storeTest['id'] = 1
storeTest['owner'] = OrderedDict([('id', 1), ('username', u'username'),
            ('first_name', u'first_name'), ('last_name', u'last_name'),
            ('email', u'email@email.com'), ('phone', u'+584241235566'),
       ('address', u'60 av, 90 st wall street'), ('type_user', u've')])

product = {
    'name': "name",
    'image': '/image.jpg/',
    'in_stock': True,
    'quantity': 5,
    'weight': u'700.00',
    'price': u'20.00',
    'category': u've'
}

productTest = {
    'name': "name",
    'image': '/image.jpg/',
    'in_stock': True,
    'quantity': 5,
    'weight': u'700.00',
    'price': u'20.00',
    'category': u've'
}
productTest = product
productTest['id'] = 1
productTest['store'] = OrderedDict([('id', 1), ('name', u'company name'),
              ('longitude', u'first_name'), ('last_name', u'last_name'),
              ('email', u'email@email.com'), ('phone', u'+584241235566'),
         ('address', u'60 av, 90 st wall street'), ('type_user', u've')])
productTest['owner'] = OrderedDict([('id', 1), ('username', u'username'),
              ('first_name', u'first_name'), ('last_name', u'last_name'),
              ('email', u'email@email.com'), ('phone', u'+584241235566'),
         ('address', u'60 av, 90 st wall street'), ('type_user', u've')])
