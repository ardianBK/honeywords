# honeywords_existing-user
This library is for CodeIgniter framework.
In this library I have successfully implemented Imran Erguler's existing user honeywords scheme [1]
You can use this for autentication library on your website.
suggestions and feedback are very useful for me (:

# How to use?
1.  Copy  'application' folder to your Codeigniter
2.  Open .../application/config/autoload.php
3.  Change $autoload['libraries'], to $autoload['libraries'] = array('honeywords','SHA3_256')
4.  You can use 'view' folder to help you understand the process on my library

# additional file
1. Fakename.txt and Kamus_Bahasa.txt is file for initialitation honeywords.
2. Fakename.txt for user decoy and Kamus_Bahasa for generating password decoy
3. passcommon_list.txt for reference so that the user does not enter the password entered into the list of common password
4. You must copy this file to .../application/libraries/
5. You can change this file as you like

# References
1. Imran Erguler. 2015. Achieving Flatness: Selecting the Honeywords from Existing User Passwords.  IEEE Transactions on Dependable and Secure Computing. -> https://ieeexplore.ieee.org/document/7047759/
2. random_compat -> https://github.com/paragonie/random_compat/releases/tag/v1.4.3
