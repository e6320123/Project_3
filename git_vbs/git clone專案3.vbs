Set WshShell = WScript.CreateObject("WScript.Shell")

Set objShell = CreateObject("Wscript.Shell") 

wscript.sleep(300)
objShell.sendkeys ("%{ESC}")

wscript.sleep(300)
WshShell.SendKeys "git clone https://github.com/e6320123/Project_3"

wscript.sleep(300)
WshShell.SendKeys "{ENTER}" 