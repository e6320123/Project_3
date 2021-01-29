
set WshShell = WScript.CreateObject("WScript.Shell")

Set objShell = CreateObject("Wscript.Shell") 


yesterday = DateAdd("d",-1,date)

ystr=year(yesterday)
mstr=Month(yesterday)
if len(mstr)<2 then mstr="0"&mstr
dstr=day(yesterday)
if len(dstr)<2 then dstr="0"&dstr

inputDate=ystr&"-"&mstr&"-"&dstr

'inputDate="2021-01-26"

wscript.sleep(1000)

WshShell.SendKeys "gosrc"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(1000)

WshShell.SendKeys "cd robot"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(1000)

WshShell.SendKeys "php rpt_ins.php "&inputDate&" jdb168"
wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  
wscript.sleep(1000)


 