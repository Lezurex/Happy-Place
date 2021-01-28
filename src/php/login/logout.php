<?php

session_start();
session_destroy();

header("Location: ../../admin?logout=1");