# gunicorn config file

bind = "0.0.0.0:8080"
workers = 3
#worker_class = 'gevent'
chdir = "/maraveca"
loglevel = "INFO"

