module.exports = {
    printWidth: 80,
    tabWidth: 4,
    useTabs: false,

    overrides: [
        {
            files: "*.php",
            options: {
                useTabs: true,
                singleQuote: true,
                trailingComma: "none",
                braceStyle: "psr-2"
            }
        }
    ]
};
