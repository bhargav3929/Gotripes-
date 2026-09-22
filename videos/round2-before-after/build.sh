#!/bin/sh
# Cut each narrated segment out of the two recordings, lay its voice line over
# it, and join them in before/after order.  Run after record.mjs (both states).
set -e
cd "$(dirname "$0")"
ORDER="01:after 02:before 03:after 04:before 05:after 06:before 07:after 08:after 09:before 10:after 11:after"
mkdir -p out/seg && : > out/seg/list.txt
for item in $ORDER; do
  id=${item%%:*}; st=${item#*:}
  read s e <<EOT
$(node -e "const m=require('./out/$st-marks.json').find(x=>x.id==='$id');console.log(m.start,m.end)")
EOT
  d=$(echo "$e - $s" | bc)
  ffmpeg -loglevel error -y -ss "$s" -t "$d" -i "out/$st.webm" -i "voice/$id.wav" \
    -filter_complex "[0:v]fps=30,format=yuv420p,fade=t=in:st=0:d=0.2[v];[1:a]adelay=300|300,apad[a]" \
    -map "[v]" -map "[a]" -t "$d" -c:v libx264 -preset medium -crf 18 -c:a aac -b:a 160k -ar 48000 "out/seg/$id.mp4"
  echo "file '$id.mp4'" >> out/seg/list.txt
  echo "$id $st $s..$e ($d s)"
done
ffmpeg -loglevel error -y -f concat -safe 0 -i out/seg/list.txt -c copy out/joined.mp4
T=$(ffprobe -v error -show_entries format=duration -of default=nw=1:nk=1 out/joined.mp4)
ffmpeg -loglevel error -y -i out/joined.mp4 -vf "fade=t=out:st=$(echo "$T - 0.8" | bc):d=0.8" -af "afade=t=out:st=$(echo "$T - 0.8" | bc):d=0.8" \
  -c:v libx264 -preset medium -crf 18 -c:a aac -b:a 160k -movflags +faststart out/gotrips-round2-before-after-2026-09-18.mp4
ffprobe -v error -show_entries format=duration,size -of default=nw=1 out/gotrips-round2-before-after-2026-09-18.mp4
