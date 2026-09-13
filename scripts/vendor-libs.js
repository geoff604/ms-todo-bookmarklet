// Copies the browser-ready builds of our npm dependencies into src/deps/,
// since this app is deployed as plain static files (no bundler / build step).
// Run automatically after `npm install`, or manually via `npm run vendor`.
const fs = require("fs");
const path = require("path");

const root = path.join(__dirname, "..");
const nodeModules = path.join(root, "node_modules");
const depsDir = path.join(root, "src", "deps");

fs.rmSync(depsDir, { recursive: true, force: true });
fs.mkdirSync(depsDir, { recursive: true });

function copyFile(src, destRelative) {
  const dest = path.join(depsDir, destRelative);
  fs.mkdirSync(path.dirname(dest), { recursive: true });
  fs.copyFileSync(src, dest);
  console.log(`vendored ${destRelative}`);
}

function copyDir(src, destRelative) {
  const dest = path.join(depsDir, destRelative);
  fs.cpSync(src, dest, { recursive: true });
  console.log(`vendored ${destRelative}/`);
}

copyFile(
  path.join(nodeModules, "jquery", "dist", "jquery.min.js"),
  "jquery.min.js"
);

copyFile(
  path.join(nodeModules, "jquery-ui", "dist", "jquery-ui.min.js"),
  "jquery-ui.min.js"
);
copyDir(
  path.join(nodeModules, "jquery-ui", "dist", "themes", "smoothness"),
  "jquery-ui-theme"
);

copyFile(
  path.join(nodeModules, "@azure", "msal-browser", "lib", "msal-browser.min.js"),
  "msal-browser.min.js"
);
copyFile(
  path.join(
    nodeModules,
    "@azure",
    "msal-browser",
    "lib",
    "redirect-bridge",
    "msal-redirect-bridge.min.js"
  ),
  "msal-redirect-bridge.min.js"
);
