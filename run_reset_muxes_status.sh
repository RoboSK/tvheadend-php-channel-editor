php -c ./php.ini -n ./index_reset_muxes_status.php

# EDIT -> change path to Tvheadend configuration
chmod 700 -R /home/hts/.hts/tvheadend/dvbmuxes/
# EDIT -> change user/group for Tvheadend
chown hts:video -R /home/hts/.hts/tvheadend/dvbmuxes/
