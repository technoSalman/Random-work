var fs = require('fs');
const mysql = require('mysql');
const readLine = require('readline');

let connection = mysql.createConnection({
	host: 'localhost',
	user: 'root',
	password: 'linux',
	database: '12StepIllinois',
});

connection.connect(function (err) {
	if (err) {
		console.error('error connecting: ' + err.stack);
		return;
	}

	console.log('connected as id ' + connection.threadId);
});

const tempArr = [];
let d = [];
async function processLineByLine() {
	const fileStream = fs.createReadStream('../Downloads/demo.txt');

	const rl = readLine.createInterface({
		input: fileStream,
		crlfDelay: Infinity,
	});
	let addvalue = false;
	for await (const line of rl) {
		if (line.trim() == 'B2492' || line.trim() == 'B2490') {
			addvalue = true;
		} else if (line.trim() == 'Infofox 929865') {
			tempArr.push(d);
			d = [];
			addvalue = false;
			// console.log(d);
		}
		if (addvalue) {
			if (
				line.trim().indexOf('Millennium') == -1 &&
				line.trim().indexOf('*User') == -1 &&
				line.trim().indexOf('disconnected') == -1
			) {
				// console.log(line);
				if (line.trim()) {
					d.push(line);
				}
				// console.log(d);
			}
		}
	}
	console.log(tempArr);
}
