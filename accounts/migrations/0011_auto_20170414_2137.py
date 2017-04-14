# -*- coding: utf-8 -*-
# Generated by Django 1.10.4 on 2017-04-14 21:37
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('accounts', '0010_auto_20170325_1804'),
    ]

    operations = [
        migrations.AlterField(
            model_name='user',
            name='type_dni',
            field=models.CharField(blank=True, choices=[('E', 'Extranjero'), ('V', 'Venezolano'), ('J', 'RIF'), ('G', 'Gubernamental')], max_length=1, null=True),
        ),
    ]
