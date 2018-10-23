module.exports = {
  root: true,
  env: {
    browser: true,
  },
  extends: ["prettier"], // activate vue related rules
  parserOptions: {
    "parser": "babel-eslint",
    "ecmaVersion": 7,
    "sourceType": "module",
    "ecmaFeatures": {
      "globalReturn": false,
      "impliedStrict": false,
      "jsx": false,
      "experimentalObjectRestSpread": false,
      "allowImportExportEverywhere": false
    }
  },
  rules: {
    // allow paren-less arrow functions
    "arrow-parens": 0,
    // allow async-await
    "generator-star-spacing": 0,
    // allow debugger during development
    "no-debugger": process.env.NODE_ENV === 'production' ? 2 : 0,
    "semi": [2, "never"],
    "quotes": [2, "single"]
  }
};
