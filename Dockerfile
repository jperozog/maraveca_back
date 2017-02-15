FROM ubuntu:14.04
MAINTAINER secnot <secnot@secnot.com>


# Actualizacion de los 'sources' a la ultima version
RUN apt-get update

# Instalar los paquetes del sistema necesarios para python
RUN apt-get install -qy python \
                        python-dev \
                        python-pip \
                        python-setuptools \
                        build-essential

# Instalar algunas utilidades extras (opcional)
RUN apt-get install -qy vim \
                        wget \
                        net-tools \
                        git

# Instalamos resto aplicaciones
RUN apt-get install -qy nginx \
			supervisor


###############################
#				
#        Nginx
#
###############################

# Copiamos la configuracion de nginx
ADD nginx-default /etc/nginx/sites-available/default

# Se desactiva el modo demonio para arrancar el proceso con supervisor
RUN echo "\ndaemon off;" >> /etc/nginx/nginx.conf

# Cambiar usuario de root a www-data (por defecto en ubuntu 14.04)
# RUN echo "user www-data;" >> /etc/nginx/nginx.conf

# Permisos
RUN chown -R www-data:www-data /var/lib/nginx

# Permitimos el acceso al puerto 80 del contenedor
EXPOSE 80


##################################
#
#        Gunicorn y Django
#
################################

# Copiar aplicacion del subdirectorio django_app/ al directorio
# /django_app en el contenedor
ADD maraveca /maraveca
RUN chown -R www-data:www-data /maraveca

# Si la aplicacion tiene dependencias de paquetes del del sistema
# este es un buen sitio para instalarlas, por ejemplo:
# RUN apt-get install -qy python-dev libjpeg-dev zlib1g-dev

# Usamos requirements.txt para instalar las dependencias de la
# aplicacion.
RUN pip install -r /maraveca/requirements.txt

# Tambien se pueden instalar individualmente, por ejemplo:
# RUN pip install Django
# RUN pip install bleach
# ...

# Una buena medida de seguridad es alamacenar claves usuarios y 
# otras credenciales de seguridad en variables de entorno.
# Se importan desde settings.py con:
# 	PAYPAL_CLIENT_ID     = os.environ['PAYPAL_CLIENT_ID']
#	PAYPAL_CLIENT_SECRET = os.environ['PAYPAL_CLIENT_SECRET']
# ENV PAYPAL_CLIENT_ID sdfasFASDRwefasFqasdfAsdfAsdFAsdfsDFaSDfWERtSDFg
# ENV PAYPAL_CLIENT_SECRET ASAsdfarasDFaRasdFaSsdfghJdfGHDGsdTRSDfGErtAFSD

# Como precaucion se instala gunicorn, aunque deberia estar en 
# requirements.txt
RUN pip install gunicorn

# Por ultimo se copia la configuracion de gunicorn.
ADD gunicorn-config.py /etc/gunicorn/config.py


#############################
#
#        Supervisor
#
############################

# Copiar la configuracion 
ADD supervisor.conf /etc/supervisor/conf.d/maraveca.conf

# Instalamos supervisor-stdout para que los logs de supervisor, sean impresos 
# en stdout, asi podran ser grabados fuera del contenedor sin necesidad
# de montar volumenes. (ver supervisor.conf)
RUN pip install supervisor-stdout

# Establecer el directorio de trabajo
WORKDIR /maraveca

# Comando por defecto que se ejecutara al arranque del contenedor, supervisor
# se encarga de gestionar nginx y gunicorn.
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

