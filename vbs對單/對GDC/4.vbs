
set WshShell = WScript.CreateObject("WScript.Shell")

Set objShell = CreateObject("Wscript.Shell") 


yesterday = DateAdd("d",-1,date)

ystr=year(yesterday)
mstr=Month(yesterday)
if len(mstr)<2 then mstr="0"&mstr
dstr=day(yesterday)
if len(dstr)<2 then dstr="0"&dstr

inputDate=ystr&"-"&mstr&"-"&dstr


wscript.sleep(1000)

WshShell.SendKeys "php checkreport.php"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(200)

WshShell.SendKeys "ag"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(200)

WshShell.SendKeys "1"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(200)

WshShell.SendKeys "4"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  

WshShell.SendKeys "1"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
 