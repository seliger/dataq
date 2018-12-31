# DataQ

DataQ is a simple web service that returns a line out of a text file.

### Rationale

I have a cluster of nodes that are processing a list of items. Each node needs to grab the next value from a text file. Rather than setting up some sort of complicated queuing or messaging platform, this was quicker and easier to implement. 

## Installation

This application requires a web server such as [Apache](https://httpd.apache.org), [NGINX](https://www.nginx.com), [IIS](https://www.iis.net), or others. It also requires [PHP](https://www.php.net) to be installed as well. 

This was developed against PHP 7.2, and should work with any currently supported PHP versions. This does not require anything beyond core PHP support. 


### Getting Started

 * Clone this repo or copy the files into a subfolder in your web server. Within that directory, make two sub directories called "data" and "state". 

 * Set the "state" directory so that it is writable by the web server. 

 * Copy your text files into the "data" directory. They must have the extension ".txt".


## Usage

To invoke the web service, call it as such:

```
https://your.web.server/dataq/fetch.php?job=XXXX&data=YYYY
```

* **job** is a unique number for a given job. This is so you can run multiple instances at the same time.
* **data** is the front part of the file name. For example, if you have cities.txt, you would specifiy cities here.

A job number is unique to it's data. So if you had "cities" and "countries" they could both have "1" as a valid job number.


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.


## License
[MIT](https://choosealicense.com/licenses/mit/)