=0; for i in 2015-mai*.jpg; do let a=a+1; b=`printf 2015-mai-%02d.jpg $a`; echo "mv $i $b"; done
