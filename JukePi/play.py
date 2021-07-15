import os
import vlc,shutil, time, requests, gc
from subprocess import call

finish = 0

url = 'https://qroo.co.tz/jukebox/index2.php?task=FetchSong'
response = requests.get(url, headers={"User-Agent":"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1"})
song = response.content.decode("utf-8")

def is_not_blank(s):
    return bool(s and not s.isspace())

def SongFinished(event):
    global finish
    print("\nEvent reports - finished")
    finish = 1

    call(["python3", "play.py"])
#check if queue is empty
queue = is_not_blank(song) 

#play music if available
if queue  == True:
    #play song
    Instance = vlc.Instance()
    player = Instance.media_player_new()
    Media = Instance.media_new(song)
    Media.get_mrl()
    player.set_media(Media)
    events = player.event_manager()
    events.event_attach(vlc.EventType.MediaPlayerEndReached, SongFinished)

    player.play()
    player.audio_set_volume(60)

    while finish == 0:
        time.sleep(0.5)

        #Garbage collection
        del response, song, queue, Instance, player, Media, events
        gc.collect()
else:
    print("No music in queue")
    time.sleep(0.5)
    #Garbage collection then re call app
    del url, response, song, queue
    gc.collect()
    call(["python3", "play.py"])