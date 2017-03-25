# -*- coding: utf-8 -*-
# Generated by Django 1.10.4 on 2016-02-11 21:14
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('services', '0002_auto_20160211_2108'),
    ]

    operations = [
        migrations.AlterField(
            model_name='service',
            name='install_date',
            field=models.DateTimeField(),
        ),
        migrations.AlterField(
            model_name='service',
            name='invoice_date',
            field=models.DateTimeField(),
        ),
        migrations.AlterField(
            model_name='service',
            name='monthly_type',
            field=models.CharField(choices=[('dp', 'in')], max_length=2),
        ),
        migrations.AlterField(
            model_name='service',
            name='start_date',
            field=models.DateTimeField(),
        ),
    ]
