const express = require('express');
const path = require('path');
const fs = require('fs');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// Serve static site files from the current directory
app.use(express.static(path.join(__dirname)));

// Simple file-based persistence (JSON files)
function readJSON(filename) {
  try {
    const raw = fs.readFileSync(path.join(__dirname, filename), 'utf8');
    return JSON.parse(raw || '[]');
  } catch (e) {
    return [];
  }
}

function writeJSON(filename, data) {
  fs.writeFileSync(path.join(__dirname, filename), JSON.stringify(data, null, 2));
}

// Endpoints
// POST /api/orders -> create a fuel order
app.post('/api/orders', (req, res) => {
  const orders = readJSON('orders.json');
  const id = orders.length ? orders[orders.length - 1].id + 1 : 1;
  const now = new Date().toISOString();
  const order = Object.assign({ id, createdAt: now }, req.body);
  orders.push(order);
  writeJSON('orders.json', orders);
  res.status(201).json({ success: true, order });
});

// POST /api/contact -> contact messages
app.post('/api/contact', (req, res) => {
  const items = readJSON('contacts.json');
  const id = items.length ? items[items.length - 1].id + 1 : 1;
  const msg = Object.assign({ id, createdAt: new Date().toISOString() }, req.body);
  items.push(msg);
  writeJSON('contacts.json', items);
  res.status(201).json({ success: true, message: 'Received' });
});

// POST /api/apply -> job applications
app.post('/api/apply', (req, res) => {
  const items = readJSON('applications.json');
  const id = items.length ? items[items.length - 1].id + 1 : 1;
  const appObj = Object.assign({ id, createdAt: new Date().toISOString() }, req.body);
  items.push(appObj);
  writeJSON('applications.json', items);
  res.status(201).json({ success: true, application: appObj });
});

// Basic status endpoint
app.get('/api/status', (req, res) => res.json({ status: 'ok', timestamp: new Date().toISOString() }));

app.listen(PORT, () => {
  console.log(`Server listening on http://localhost:${PORT}`);
});
