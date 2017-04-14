# -*- coding: utf-8 -*-
# Generated by Django 1.10.4 on 2017-04-14 22:16
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('services', '0009_auto_20170414_2137'),
    ]

    operations = [
        migrations.AddField(
            model_name='service',
            name='address',
            field=models.TextField(blank=True, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='celdaAP',
            field=models.CharField(blank=True, choices=[('01', 'Celda 1'), ('02', 'Celda 2')], max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='email_alt',
            field=models.EmailField(blank=True, max_length=254, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='equipment',
            field=models.CharField(blank=True, max_length=20, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='ip',
            field=models.CharField(blank=True, max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='mac',
            field=models.CharField(blank=True, max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='phoneSMS',
            field=models.CharField(blank=True, max_length=20, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='phones',
            field=models.CharField(blank=True, max_length=20, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='serial',
            field=models.CharField(blank=True, max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='server',
            field=models.CharField(blank=True, choices=[('01', 'Server 1'), ('02', 'Server 2')], max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='so',
            field=models.CharField(blank=True, choices=[('01', 'S.O 1'), ('02', 'S.O 2')], max_length=2, null=True),
        ),
        migrations.AddField(
            model_name='service',
            name='type_ip',
            field=models.CharField(choices=[('ES', 'Estatica'), ('DI', 'Dinamica')], default='DI', max_length=2),
        ),
        migrations.AlterField(
            model_name='service',
            name='comments',
            field=models.CharField(blank=True, max_length=2, null=True),
        ),
    ]