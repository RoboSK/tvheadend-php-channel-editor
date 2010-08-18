php -c ./php.ini -n ./index.php

# EDIT -> change path to Tvheadend configuration
chmod 700 -R /home/hts/.hts/tvheadend/v4lservices/
# EDIT -> change user/group for Tvheadend
chown hts:video -R /home/hts/.hts/tvheadend/v4lservices/
