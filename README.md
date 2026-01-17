# Audax Development Research Notes - 2 (ADRN-2)
## Measuring Energy Efficiency of Architectural Complexity in PHP Frameworks

**Author:** Andrea Davanzo

**ORCID:** [0009-0000-5170-1737](https://orcid.org/0009-0000-5170-1737)

**License:** MPL-2.0

## Overview
This repository contains the experimental data and orchestration scripts used to investigate the relationship between software architectural complexity and electrical power draw.

Building upon the **Accidental Complexity Score (ACS)** defined in my previous work, this study measures the energy footprint of six PHP frameworks (Laravel, Symfony, Laminas, CodeIgniter 4, Yii, and Fat-Free) using Intel RAPL telemetry.

## Repository Structure
* `framework/`: PHP frameworks under test
* `log/`:
    * `single/`: Raw output files for individual repetitions (3 runs per configuration).
    * `unified/`: Merged CSV files containing the aggregated results for each delay tier.
* `script/`:
    * `run_all.sh`: Master script for sequential batch testing.
    * `stats.php`: Generate basic stats for the CSV file.
    * `test_frameworks.sh`: Orchestration script for remote hardware preparation and HTTP workload generation.
* `VERSION`: Current version of the dataset and scripts.

## Methodology & Framework Setup
The frameworks used in this study are the same as those in [Audax Development Research Notes - 1 (ADRN-1)](https://doi.org/10.5281/zenodo.17690007).

**Key modifications for energy measurement:**
1. **Instrumentation Removal:** All calls to the previous measurement function (`cesp_log()`) were removed to eliminate I/O and CPU noise, ensuring RAPL readings reflect only the framework's native overhead.
2. **Hardware:** Tested on an Intel Core i3-6100T (Power-optimized) with Turbo Boost disabled and CPU governor set to `performance`.
3. **Cold Starts:** Services (`php-fpm` and `apache2`) are restarted before every test to ensure the energy cost of structural loading is captured without OpCode cache interference.
4. **Test duration:** Each test has been executed for 300 seconds

## Data Schema
Logs are split into two types:
* `*_events.csv`: Local logs of HTTP request timestamps and status codes.
* `*_rapl.csv`: Remote telemetry containing Joules (μJ), Wattage (W), and CPU frequency captured at 5-seconds.

## Citation
You can cite all versions by using the DOI 10.5281/zenodo.18278575. This DOI represents all versions, and will always resolve to the latest one.