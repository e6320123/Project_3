
set WshShell = WScript.CreateObject("WScript.Shell")

Set objShell = CreateObject("Wscript.Shell") 


yesterday = DateAdd("d",-1,date)

ystr=year(yesterday)
mstr=Month(yesterday)
if len(mstr)<2 then mstr="0"&mstr
dstr=day(yesterday)
if len(dstr)<2 then dstr="0"&dstr

inputDate=ystr&"-"&mstr&"-"&dstr

 

' WshShell.Run "%windir%\system32\cmd.exe"
WshShell.Run "C:\Users\Ben\AppData\Local\Microsoft\WindowsApps\wt.exe"
wscript.sleep(1000)


WshShell.SendKeys "cd C:\Users\Ben\Desktop\report\ebet"

wscript.sleep(200)
WshShell.SendKeys "{ENTER}"

wscript.sleep(200)
WshShell.SendKeys "php EBET.class.php "&inputDate&" "&inputDate

wscript.sleep(200)
WshShell.SendKeys "{ENTER}"  