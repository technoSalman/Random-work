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

	let arr = [];
	tempArr.forEach((item) => {
		let address = item[1];
		let phone = item[2];
		// item[3].length >= 25 ?
		let name = item[3];
		let fullAddress = item[4];
		let newAddress = fullAddress?.split(' ');
		let city = '';
		let state = '';
		let zip = '';
		let count = 0;
		newAddress?.forEach((row) => {
			count = count + 1;
			if (count != newAddress.length) {
				if (row.length == 2) {
					state = row;
				} else {
					city += row + ' ';
				}
			} else {
				zip = row + ' ';
			}
		});
		let obj = [];
		obj['name'] = name.trim();
		obj['address'] = address.trim();
		obj['phone'] = phone.trim();
		obj['city'] = city.trim();
		obj['state'] = state.trim();
		obj['zip'] = zip.trim();
		arr.push(obj);
	});
	// console.log(arr);
	let sql =
		'INSERT INTO userdata(name, phone, address, city, state, zip) VALUES ?';
	connection.query(
		sql,
		[
			arr.map((elem, index) => [
				arr[index].name,
				arr[index].phone,
				arr[index].address,
				arr[index].city,
				arr[index].state,
				arr[index].zip,
			]),
		],
		(err) => {
			if (err) {
				console.log(err);
			}
		}
	);
}
processLineByLine();
