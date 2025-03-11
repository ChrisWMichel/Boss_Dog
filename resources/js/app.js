import "./bootstrap";

import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import collapse from "@alpinejs/collapse";
//import axios from "axios";
import { get, post, request } from "./http.js";

Alpine.plugin(persist);
Alpine.plugin(collapse);

window.Alpine = Alpine;

document.addEventListener("alpine:init", () => {
    Alpine.store("header", {
        cartItemsObject: Alpine.$persist({}),
        watchingItems: Alpine.$persist([]),
        get watchlistItems() {
            return this.watchingItems.length;
        },
        get cartItems() {
            return Object.values(this.cartItemsObject || {}).reduce(
                (accum, next) => accum + parseInt(next.quantity),
                0
            );
        },
    });

    Alpine.data("toast", () => ({
        visible: false,
        delay: 5000,
        percent: 0,
        interval: null,
        timeout: null,
        message: null,
        close() {
            this.visible = false;
            clearInterval(this.interval);
        },
        show(message) {
            this.visible = true;
            this.message = message;

            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }

            this.timeout = setTimeout(() => {
                this.visible = false;
                this.timeout = null;
            }, this.delay);
            const startDate = Date.now();
            const futureDate = Date.now() + this.delay;
            this.interval = setInterval(() => {
                const date = Date.now();
                this.percent =
                    ((date - startDate) * 100) / (futureDate - startDate);
                if (this.percent >= 100) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
            }, 30);
        },
    }));

    Alpine.data("productItem", (product) => {
        return {
            id: product.id,
            product,
            get watchlistItems() {
                return this.$store.watchlistItems;
            },

            addToWatchlist() {
                if (this.isInWatchlist()) {
                    this.$store.header.watchingItems.splice(
                        this.$store.header.watchingItems.findIndex(
                            (p) => p.id === product.id
                        ),
                        1
                    );
                    this.$dispatch("notify", {
                        message: "The item was removed from your watchlist",
                    });
                } else {
                    this.$store.header.watchingItems.push(product);
                    this.$dispatch("notify", {
                        message: "The item was added into the watchlist",
                    });
                }
            },
            isInWatchlist() {
                return this.$store.header.watchingItems.find(
                    (p) => p.id === product.id
                );
            },
            addToCart(quantity = 1) {
                post(this.product.addToCartUrl, { quantity })
                    .then((response) => {
                        this.$store.header.cartItemsObject[this.product.id] = {
                            ...this.product,
                            quantity:
                                (this.$store.header.cartItemsObject[
                                    this.product.id
                                ]?.quantity || 0) + quantity,
                        };
                        this.$dispatch("cart-change", {
                            count: response.count,
                        });
                        this.$dispatch("notify", {
                            message: "The item was added into the cart",
                        });
                    })
                    .catch((response) => {
                        console.log(response);
                        this.$dispatch("notify", {
                            message:
                                response.message ||
                                "Server Error. Please try again.",
                            type: "error",
                        });
                    });
            },
            removeItemFromCart() {
                console.log("removeItemFromCart called");
                post(this.product.removeUrl)
                    .then((response) => {
                        this.$dispatch("notify", {
                            message: "The item was removed from cart",
                        });
                        this.$dispatch("cart-change", {
                            count: response.count,
                        });
                        this.cartItems = this.cartItems.filter(
                            (p) => p.id !== product.id
                        );
                        console.log("The item was removed from cart");
                        // this.$dispatch("notify", {
                        //     message: "The item was removed from cart",
                        // });
                    })
                    .catch((response) => {
                        console.error(
                            "Error removing item from cart:",
                            response
                        ); // Debugging statement
                    });
            },
            changeQuantity() {
                post(this.product.updateQuantityUrl, {
                    quantity: product.quantity,
                }).then((response) => {
                    if (!this.$store.header.cartItemsObject[this.product.id]) {
                        this.$store.header.cartItemsObject[this.product.id] =
                            {};
                    }
                    console.log("changeQuantity");
                    this.$store.header.cartItemsObject[
                        this.product.id
                    ].quantity = product.quantity;
                    this.$dispatch("cart-change", {
                        count: response.count,
                    });
                    this.$dispatch("notify", {
                        message: "The quantity was updated",
                    });
                });
            },
        };
    });

    Alpine.data("signupForm", () => ({
        defaultClasses: "focus:ring-purple-500 focus:border-purple-500",
        errorClasses:
            "border-red-600 focus:border-red-600 ring-1 ring-red-600 focus:ring-red-600",
        successClasses:
            "border-emerald-500 focus:border-emerald-500 ring-1 ring-emerald-500 focus:ring-emerald-500",

        form: {
            firstname: "",
            lastname: "",
            email: "",
            password: "",
            password_repeat: "",
        },
        errors: {
            firstname: "",
            lastname: "",
            email: "",
            password: "",
            password_repeat: "",
        },
        submit() {
            console.log(this.form);
            this.validateFirstname();
            this.validateLastname();
            this.validateEmail();
            this.validatePassword();
            this.validatePasswordRepeat();
            // Ensure form submission is not prevented
            if (
                !this.errors.firstname &&
                !this.errors.lastname &&
                !this.errors.email &&
                !this.errors.password &&
                !this.errors.password_repeat
            ) {
                console.log("Form submitted - app.js");
                this.$el.submit();
            }
        },
        validateFirstname() {
            this.errors.firstname = "";

            if (!this.form.firstname) {
                this.errors.firstname = "This field is required";
            } else if (this.form.firstname.length < 2) {
                this.errors.firstname =
                    "The first name should be at least two characters";
            }
        },
        validateLastname() {
            this.errors.lastname = "";

            if (!this.form.lastname) {
                this.errors.lastname = "This field is required";
            } else if (this.form.lastname.length < 2) {
                this.errors.lastname =
                    "The last name should be at least two characters";
            }
        },
        validateEmail() {
            this.errors.email = "";

            if (!this.form.email) {
                this.errors.email = "This field is required";
            } else if (!this.validateEmailWithRegex()) {
                this.errors.email = "This must be a valid email field";
            }
        },
        validateEmailWithRegex() {
            return /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                this.form.email
            );
        },
        validatePassword() {
            this.errors.password = "";

            if (!this.form.password) {
                this.errors.password = "This field is required";
            }
        },
        validatePasswordRepeat() {
            this.errors.password_repeat = "";

            if (!this.form.password_repeat) {
                this.errors.password_repeat = "This field is required";
            }
        },
    }));
});

Alpine.start();
