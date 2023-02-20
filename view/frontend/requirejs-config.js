var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Bsitc_Brightpearl/js/view/shipping': true
            }
        }
    },
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Bsitc_Brightpearl/js/shipping-save-processor"
        }
    }
};
