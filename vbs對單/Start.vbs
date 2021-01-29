
set WshShell = WScript.CreateObject("WScript.Shell")

Set objShell = CreateObject("Wscript.Shell") 


yesterday = DateAdd("d",-1,date)

ystr=year(yesterday)
mstr=Month(yesterday)
if len(mstr)<2 then mstr="0"&mstr
dstr=day(yesterday)
if len(dstr)<2 then dstr="0"&dstr

inputDate=ystr&"-"&mstr&"-"&dstr

 


  
WshShell.Run "C:\Program Files (x86)\WinSCP\WinSCP.exe"
wscript.sleep(300)

WshShell.Run "C:\Program Files\OpenVPN\bin\openvpn-gui.exe"
wscript.sleep(300)

WshShell.Run "C:\Users\Ben\AppData\Roaming\Telegram Desktop\Telegram.exe"
wscript.sleep(300)

WshShell.Run "C:\Program Files\PremiumSoft\Navicat 15 for MySQL\navicat.exe"
wscript.sleep(300)

WshShell.Run "C:\Users\Ben\Desktop\Telegram\Telegram.exe"
wscript.sleep(300)
 