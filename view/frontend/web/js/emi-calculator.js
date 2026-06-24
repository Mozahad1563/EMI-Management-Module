/**
 * Brain Station 23
 *
 * @category   BrainStation23
 * @package    EmiManagement
 * @author     Brain Station 23
 * @copyright  Copyright (c) 2026 Brain Station 23
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var $widget = $(element);
        var productPrice = parseFloat(config.productPrice) || 0;
        var productName = config.productName || '';
        var bankData = config.bankData || [];

        var activeBankId = null;
        var activeTenureMonths = null;

        var $triggerText = $widget.find('[data-role="trigger-text"]');
        var $viewPlansBtn = $widget.find('[data-role="view-plans"]');
        var $modal = $widget.find('[data-role="modal"]');
        var $modalBackdrop = $widget.find('[data-role="modal-backdrop"]');
        var $closeBtn = $widget.find('[data-role="close-modal"]');
        var $productNameEl = $widget.find('[data-role="product-name"]');
        var $modalPrice = $widget.find('[data-role="modal-price"]');
        var $bankList = $widget.find('[data-role="bank-list"]');
        var $tenureList = $widget.find('[data-role="tenure-list"]');
        var $monthlyPayment = $widget.find('[data-role="monthly-payment"]');
        var $interestBadge = $widget.find('[data-role="interest-badge"]');
        var $breakdownPrice = $widget.find('[data-role="breakdown-price"]');
        var $breakdownInterest = $widget.find('[data-role="breakdown-interest"]');
        var $breakdownTotal = $widget.find('[data-role="breakdown-total"]');

        function calculateEMI(price, months, rate) {
            var totalInterest = price * rate;
            var totalPayable = price + totalInterest;
            var monthlyInstallment = totalPayable / months;

            return {
                interestCharged: totalInterest,
                totalPayable: totalPayable,
                monthlyInstallment: monthlyInstallment
            };
        }

        function formatTaka(amount) {
            var rounded = Math.round(amount);
            var formatted = new Intl.NumberFormat('en-IN', {maximumFractionDigits: 0}).format(rounded);

            return '৳' + formatted;
        }

        function getBankInitials(name) {
            return name
                .split(' ')
                .map(function (w) { return w.charAt(0); })
                .join('')
                .slice(0, 3)
                .toUpperCase();
        }

        function findBank(bankId) {
            var result = null;

            $.each(bankData, function (_, b) {
                if (b.bank_id === bankId) {
                    result = b;
                    return false;
                }
            });

            return result;
        }

        function initWidget() {
            if (!bankData.length) {
                $triggerText.text('No EMI plans are currently configured.');
                $viewPlansBtn.hide();
                return;
            }

            var lowestEMI = Infinity;
            var isAnyEligible = false;
            var minOrderAmount = Infinity;

            $.each(bankData, function (_, bank) {
                if (bank.min_amount < minOrderAmount) {
                    minOrderAmount = bank.min_amount;
                }

                if (productPrice >= bank.min_amount) {
                    isAnyEligible = true;

                    $.each(bank.tenures, function (_, tenure) {
                        var result = calculateEMI(productPrice, tenure.months, tenure.interest_rate);

                        if (result.monthlyInstallment < lowestEMI) {
                            lowestEMI = result.monthlyInstallment;
                        }
                    });
                }
            });

            if (isAnyEligible && lowestEMI !== Infinity) {
                $triggerText
                    .empty()
                    .append(document.createTextNode('EMI options available starting from '))
                    .append(
                        $('<span>', {'class': 'emi-trigger__text-highlight'})
                            .text(formatTaka(lowestEMI) + '/month')
                    )
                    .append(document.createTextNode('.'));
                $viewPlansBtn.show();
            } else {
                $triggerText
                    .empty()
                    .append(document.createTextNode('EMI available on orders over '))
                    .append(
                        $('<span>', {'class': 'emi-trigger__text-highlight'})
                            .text(formatTaka(minOrderAmount))
                    )
                    .append(document.createTextNode('.'));
                $viewPlansBtn.hide();
            }
        }

        function openModal() {
            $modal.css('display', 'flex');
            $modal[0].offsetHeight; // force reflow for CSS transition
            $modal.addClass('is-active');
            $('body').css('overflow', 'hidden');

            $productNameEl.text(productName);
            $modalPrice.text(formatTaka(productPrice));

            var firstEligible = null;

            $.each(bankData, function (_, bank) {
                if (productPrice >= bank.min_amount) {
                    firstEligible = bank;
                    return false;
                }
            });

            if (firstEligible) {
                selectBank(firstEligible.bank_id);
            } else {
                renderBanks();
            }
        }

        function closeModal() {
            $modal.removeClass('is-active');
            setTimeout(function () {
                $modal.css('display', 'none');
            }, 300);
            $('body').css('overflow', '');
        }

        function renderBanks() {
            $bankList.empty();

            $.each(bankData, function (_, bank) {
                var isDisabled = productPrice < bank.min_amount;
                var isSelected = bank.bank_id === activeBankId;
                var classes = 'emi-bank-card';

                if (isDisabled) {
                    classes += ' is-disabled';
                }

                if (isSelected) {
                    classes += ' is-selected';
                }

                var $card = $('<div>', {'class': classes});

                if (bank.logo_url && bank.logo_url.trim()) {
                    var $logo = $('<img>', {
                        'src': bank.logo_url,
                        'alt': bank.bank_name,
                        'class': 'emi-bank-card__logo'
                    }).on('error', function () {
                        $(this).hide().next('.emi-bank-card__initials').show();
                    });

                    $card.append($logo);
                }

                var $initials = $('<div>', {
                    'class': 'emi-bank-card__initials',
                    'text': getBankInitials(bank.bank_name)
                });

                if (bank.logo_url && bank.logo_url.trim()) {
                    $initials.hide();
                }

                $card.append($initials);
                $card.append($('<span>', {'class': 'emi-bank-card__name', 'text': bank.bank_name}));

                if (isDisabled) {
                    $card.append($('<span>', {
                        'class': 'emi-bank-card__min-order',
                        'text': 'Min order ' + formatTaka(bank.min_amount)
                    }));
                }

                if (!isDisabled) {
                    (function (id) {
                        $card.on('click', function () {
                            selectBank(id);
                        });
                    })(bank.bank_id);
                }

                $bankList.append($card);
            });
        }

        function renderTenures() {
            $tenureList.empty();
            var bank = findBank(activeBankId);

            if (!bank) {
                return;
            }

            $.each(bank.tenures, function (_, tenure) {
                var isSelected = tenure.months === activeTenureMonths;
                var classes = 'emi-tenure-chip' + (isSelected ? ' is-selected' : '');
                var label = tenure.months + ' ' + (tenure.months === 1 ? 'Month' : 'Months');

                var $chip = $('<button>', {
                    'class': classes,
                    'type': 'button',
                    'text': label
                });

                (function (months) {
                    $chip.on('click', function () {
                        selectTenure(months);
                    });
                })(tenure.months);

                $tenureList.append($chip);
            });
        }

        function selectBank(bankId) {
            activeBankId = bankId;
            var bank = findBank(bankId);

            activeTenureMonths = (bank && bank.tenures.length) ? bank.tenures[0].months : null;

            renderBanks();
            renderTenures();
            updateCalculations();
        }

        function selectTenure(months) {
            activeTenureMonths = months;
            renderTenures();
            updateCalculations();
        }

        function updateCalculations() {
            var bank = findBank(activeBankId);

            if (!bank || activeTenureMonths === null) {
                resetCalculations();
                return;
            }

            var tenure = null;

            $.each(bank.tenures, function (_, t) {
                if (t.months === activeTenureMonths) {
                    tenure = t;
                    return false;
                }
            });

            if (!tenure) {
                resetCalculations();
                return;
            }

            var result = calculateEMI(productPrice, tenure.months, tenure.interest_rate);

            $monthlyPayment.text(formatTaka(result.monthlyInstallment));
            $breakdownPrice.text(formatTaka(productPrice));
            $breakdownInterest.text(formatTaka(result.interestCharged));
            $breakdownTotal.text(formatTaka(result.totalPayable));

            if (tenure.interest_rate === 0) {
                $interestBadge
                    .text('0% Interest EMI')
                    .attr('class', 'emi-badge emi-badge--success');
            } else {
                $interestBadge
                    .text(Math.round(tenure.interest_rate * 100) + '% Regular Interest')
                    .attr('class', 'emi-badge emi-badge--warning');
            }
        }

        function resetCalculations() {
            $monthlyPayment.text(formatTaka(0));
            $breakdownPrice.text(formatTaka(0));
            $breakdownInterest.text(formatTaka(0));
            $breakdownTotal.text(formatTaka(0));
            $interestBadge
                .text('N/A')
                .attr('class', 'emi-badge emi-badge--neutral');
        }

        // Event bindings
        $viewPlansBtn.on('click', openModal);
        $modalBackdrop.on('click', closeModal);
        $closeBtn.on('click', closeModal);

        $(document).on('keydown.emiCalculator', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        initWidget();
    };
});
