# CurriculumVolantino
The purpose of this repository is to offer an overview of my skills.
The project involves the creation of a simple REST call for the management of leaflets. 
For the realization the indications given by the analysis reported below were followed.
It is to be considered my first approach with Framework cackePHP.

## Stack

 - Php 7
 - Framework cackePHP 4
 - Apache server
 - Docker

## Usage
### Apache context:
upload the folder app_volantini  inside the Apache htdocs folder, otherwise configure the virtualhost indicating the folder.
reload server and call the application.

### Docker context:
TODO

### EndPoints links examples
http://**{{HOSTNAME}}**/flyers.json?page=1&filter[is_published]=0

### EndPoint wrong link
http://volantini.lcl/flyers.json?page=1&filter[is_published]=0&fields=fooo
http://volantini.lcl/flyers.json?page=1&filter[is_published]=0&filter[foo]=bar
