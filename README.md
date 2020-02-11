# Tiny Voting System
A tiny voting system for anyone who has a local webserver

## Requirements
To run this script on your system you need the following:
- Webserver 
- PHP Interpreter
- Copy the files in the webserver directory

I use on my local windows(c) computer the XAMPP application 
(see https://www.apachefriends.org/de/index.html).
The webserver directory is ...\xampp\htdocs.

## How to use 
Each voting has its own CSV file. The naming convention
of this file is `XXXX_Title_Of_Voting.csv` (XXXX is a number).

The content of the example file `0001_Voting_President.csv`
look like this:

&nbsp;  |09.05./10.05.|16.05./17.05.|23.05./24.05.
--|--------------|-------------|-------------
Trump, Donald|0|0|0
Obama, Barack|0|0|0
Bush, George|0|0|0
Clinton, Bill|0|0|0
Bush, George|0|0|0
Reagan, Ronald|0|0|0

Each item is separated from each other by the character `;`.

You can also edit the CSV file with Microsoft(c) Excel
but if using the german excel version, the separation character
will be `,`!




