#! /bin/bash
#
# This script converts files stored in WAV format to MP3.
# It also updates the twitterqueue table after the conversion is completed.
#
# Change to directory holding WAV files
cd $1

# Set up variable to hold WAV file names
FILES="*.wav"

for f in $FILES
do

# Get the unique 32 character file name
FILENAME=$(echo $f | cut -c 1-32)
 
# Convert the WAV file to MP3 and move it to the MP3 directory
sox -t wav -v 1.5 "$FILENAME.wav" -t wav -s -d -c 1 -| lame -b 80 - "$FILENAME.mp3"
mv "$FILENAME.mp3" ../mp3s/"$FILENAME.mp3"
rm -f "$FILENAME.wav"

# Update the Twitter queue to indicate the message is ready to be sent
mysql -h localhost -u $2 -p$3 -D $4 -e "UPDATE twitter_queue SET status = 1 WHERE messageid = '$FILENAME';"

done
