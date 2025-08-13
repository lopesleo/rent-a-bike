import tsEslintParser from "@typescript-eslint/parser";
import tsEslintPlugin from "@typescript-eslint/eslint-plugin";
import eslintPluginPrettier from "eslint-plugin-prettier";

export default [
    {
        files: ["**/*.{ts,tsx,js,jsx}"],
        ignores: ["**/*.d.ts", "dist/**", "node_modules/**"],
        languageOptions: {
            parser: tsEslintParser,
            parserOptions: {
                project: "./tsconfig.eslint.json",
                tsconfigRootDir: process.cwd(),
            },
        },
        plugins: {
            "@typescript-eslint": tsEslintPlugin,
            prettier: eslintPluginPrettier,
        },
        rules: {
            "prettier/prettier": "error",
            "no-unused-vars": "off",
            "@typescript-eslint/no-unused-vars": [
                "warn",
                {
                    argsIgnorePattern: "^_",
                    varsIgnorePattern: "^_",
                    caughtErrorsIgnorePattern: "^_",
                },
            ],
            "@typescript-eslint/no-explicit-any": "warn",
        },
    },
];
