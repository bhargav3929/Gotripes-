#!/bin/sh
# Cut each narrated segment from the recording, lay its voice line over it and
# join them. Run after record.mjs.
set -e
cd "$(dirname "$0")"
mkdir -p out/seg && : > out/seg/list.txt
for id in 01 02 03 04 05 06 07 08 09 10 11; do
  read s e <<EOT
$(node -e "const m=require('./out/marks.json').find(x=>x.id==='$id');console.log(m.start,m.end)")
EOT
  d=$(echo "$e - $s" | bc)
  ffmpeg -loglevel error -y -ss "$s" -t "$d" -i out/session.webm -i "voice/$id.wav" \
    -filter_complex "[0:v]fps=30,scale=1600:1000,format=yuv420p,fade=t=in:st=0:d=0.2[v];[1:a]adelay=300|300,apad[a]" \
    -map "[v]" -map "[a]" -t "$d" -c:v libx264 -preset medium -crf 19 -c:a aac -b:a 160k -ar 48000 "out/seg/$id.mp4"
  echo "file '$id.mp4'" >> out/seg/list.txt
  echo "$id $s..$e ($d s)"
done
ffmpeg -loglevel error -y -f concat -safe 0 -i out/seg/list.txt -c copy out/joined.mp4
T=$(ffprobe -v error -show_entries format=duration -of default=nw=1:nk=1 out/joined.mp4)
ffmpeg -loglevel error -y -i out/joined.mp4 \
  -vf "fade=t=out:st=$(echo "$T - 0.8" | bc):d=0.8" -af "afade=t=out:st=$(echo "$T - 0.8" | bc):d=0.8" \
  -c:v libx264 -preset medium -crf 19 -c:a aac -b:a 160k -movflags +faststart \
  out/gotrips-agent-contract-2026-09-22.mp4
ffprobe -v error -show_entries format=duration,size -of default=nw=1 out/gotrips-agent-contract-2026-09-22.mp4
