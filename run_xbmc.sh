HOMEDIR=/home/hts/.hts/tvheadend/
DESTDIR=/SHARE_-_Tvheadend_XBMC/

mkdir "$DESTDIR" &>/dev/null
mkdir "$DESTDIR"XBMC/ &>/dev/null
mkdir "$DESTDIR"Tvheadend/ &>/dev/null
mkdir "$DESTDIR"LIRC/ &>/dev/null
mkdir "$DESTDIR"TV_Logo/ &>/dev/null
mkdir "$DESTDIR"LCD_TEST/ &>/dev/null
cp "$HOMEDIR"channel_list.txt "$DESTDIR"XBMC/
cp "$HOMEDIR"channel_list_xbmc.txt "$DESTDIR"XBMC/
cp "$HOMEDIR"MyTV4.db "$DESTDIR"XBMC/

# chmod 666 "$DESTDIR"XBMC/*
# chmod 666 "$DESTDIR"Tvheadend/*
# chmod 666 "$DESTDIR"LIRC/*
# chmod 666 "$DESTDIR"TV_Logo/*
# chmod 666 "$DESTDIR"LCD_TEST/*
# chmod 666 "$DESTDIR"LCD_TEST/photo/* &>/dev/null
# chmod 777 "$DESTDIR"
